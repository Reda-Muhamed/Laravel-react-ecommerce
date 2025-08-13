<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use League\Csv\Serializer\CastToArray;

class ProductVariations extends EditRecord
{

    protected static string $resource = ProductResource::class;

    protected static ?string $navigationIcon = 'heroicon-m-numbered-list';
    protected static ?string $title = 'Variation';

    public function form(Form $form): Form
    {
        $types = $this->record->variationTypes;
        $fields = [];

        foreach ($types as $type) {
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.id')->required();
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.name')->label("$type->name");
        }

        return $form
            ->schema([
                Repeater::make('variations')->addable(false)->label(false)
                    ->collapsible()
                    ->defaultItems(1)
                    ->schema([
                        Section::make()->schema($fields)->columns(3),
                        TextInput::make('quantity')->label('Quantity')->numeric(),
                        TextInput::make('price')->label('Price')->numeric(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $variations = $this->record->variations->toArray();
        $data['variations'] = $this->mergeCartesianWithExisting(
            $this->record->variationTypes,
            $variations
        );

        return $data;
    }

    private function mergeCartesianWithExisting($variationTypes, $existingData): array
    {
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;

        $cartesianProduct = $this->generateCartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);

        $mergedResult = [];

        foreach ($cartesianProduct as $product) {
            $optionsIds = collect($product)
                ->filter(fn($value, $key) => str_starts_with($key, 'variation_type_'))
                ->map(fn($option) => $option['id'])
                ->values()
                ->toArray();

            // find matching entry in existing data
            $match = array_filter($existingData, function ($existingOptions) use ($optionsIds) {
                return $existingOptions['variation_type_option_ids'] === $optionsIds;
            });

            if (!empty($match)) {
                $existingEntry = reset($match);
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
                $product['id'] = $existingEntry['id'];

            } else {
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }
            // ✅ Ensure we always keep id in merged results
            foreach ($variationTypes as $variationType) {
                $key = 'variation_type_' . $variationType->id;
                if (!isset($product[$key]['id'])) {
                    $optionId = null;
                    foreach ($variationType->options as $opt) {
                        if ($opt->name === $product[$key]['name']) {
                            $optionId = $opt->id;
                            break;
                        }
                    }
                    $product[$key]['id'] = (string) $optionId;
                }
            }


            $mergedResult[] = $product;
        }

        return $mergedResult;
    }

   private function generateCartesianProduct($variationTypes, $defaultQuantity, $defaultPrice): array
{
    if ($variationTypes instanceof \Illuminate\Support\Collection) {
        $variationTypes = $variationTypes->all();
    }

    $result = [[]];

    foreach ($variationTypes as $variationType) {
        $temp = [];
        $options = $variationType->options ?? [];

        foreach ($options as $option) {
            $optionId = is_array($option) ? $option['id'] : $option->id;
            $optionName = is_array($option) ? $option['name'] : $option->name;

            foreach ($result as $combination) {
                $newCombination = $combination + [
                    'variation_type_' . $variationType->id => [
                        'id' => (string) $optionId, // ✅ make sure ID is here
                        'name' => $optionName,
                        'label' => $variationType->name,
                    ],
                ];
                $temp[] = $newCombination;
            }
        }

        $result = $temp;
    }

    foreach ($result as &$combination) {
        if (count($combination) === count($variationTypes)) {
            $combination['quantity'] = $defaultQuantity;
            $combination['price'] = $defaultPrice;
        }
    }

    return $result;
}
    protected function mutateFormDataBeforeSave(array $data): array
    {

        $formattedData = [];

        foreach ($data['variations'] as $option) {
            $variationTypeOptionIds = [];

            foreach ($this->record->variationTypes as $variationType) {
                $fieldKey = 'variation_type_' . $variationType->id;
                if (isset($option[$fieldKey]['id'])) {
                    $variationTypeOptionIds[] = $option[$fieldKey]['id'];
                }
            }

            $formattedData[] = [
                'id'=> $option['id'],
                'variation_type_option_ids' => $variationTypeOptionIds,
                'price' => (float) ($option['price'] ?? 0),
                'quantity' => $option['quantity'] ?? 0,

            ];
        }

        $data['variations'] = $formattedData;
        return $data;
    }








    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variations = $data['variations'];
        //  dd($data['variations']);
        unset($data['variations']);
        $variations = collect($variations)->map(function ($variation) {
            return ['id'=> $variation['id'],
            'variation_type_option_ids' => json_encode($variation['variation_type_option_ids']),
            'price' => $variation['price'],
            'quantity'=> $variation['quantity'] ??0,

        ];

        })->toArray();
        // $record->update($data);
        $record->variations()->delete();
        $record->variations()->upsert($variations,['id'],['quantity','variation_type_option_ids','price']);

        return$record;
    }
}
