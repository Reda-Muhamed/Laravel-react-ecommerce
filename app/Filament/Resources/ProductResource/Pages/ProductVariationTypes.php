<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductVariationTypeEnum;
use App\Enums\ProductVariationTypesEnum;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ProductVariationTypes extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $navigationIcon = 'heroicon-m-numbered-list';
    protected static ?string $title = 'Variation Types ';

    public function form(Form $form): Form
    {

        return  $form
            ->schema([
                Repeater::make('variationTypes')->label(false)->relationship()->collapsible()->defaultItems(1)->addActionLabel('Add new variation type')->columns(2)->columnSpan(2)->schema([
                    TextInput::make('name')->required(),
                    Select::make('type')->options(ProductVariationTypeEnum::labels() ?? []),
                    Repeater::make('options')->relationship()->collapsible()->schema([
                        TextInput::make('name')->columnSpan(2)->required(),
                        SpatieMediaLibraryFileUpload::make('images')->image()
                            ->collection('images')
                            ->multiple()
                            ->openable()
                            ->panelLayout('grid')
                            ->reorderable()
                            ->appendFiles()
                            ->preserveFilenames()
                            ->columnSpan(2)
                    ])->columnSpan(2)
                ])
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
}
