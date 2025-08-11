<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatusEnum;
use App\Enums\RolesEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ProductImages;
use App\Filament\Resources\ProductResource\Pages\ProductVariationTypes;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
    protected static ?string $navigationIcon = 'heroicon-c-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->live(onBlur: true)->required()->afterStateUpdated(
                    function (string $operation, $state, callable $set) {
                        $set('slug', Str::slug($state));
                    }
                ),
                TextInput::make('slug')->required(),
                Select::make('department_id')->relationship('department', 'name')->label(__('Department'))->preload()->searchable()->required()->reactive()->afterStateUpdated(function (callable $set) {
                    $set('category_id', null);
                }),
                Select::make('category_id')->relationship(
                    name: 'category',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (Builder $query, callable $get) {
                        $departmentId = $get('department_id');
                        if ($departmentId) {
                            $query->where('department_id', $departmentId);
                        }
                    }
                )->label(__('Category'))->preload()->searchable()->required(),
                RichEditor::make('description')->required()->toolbarButtons([
                    'blockquote',
                    'bold',
                    'bulletList',
                    'h2',
                    'h3',
                    'italic',
                    'link',
                    'orderedList',
                    'redo',
                    'strike',
                    'underline',
                    'undo',
                    'table'
                ])->columnSpan(2),
                TextInput::make('price')->required()->numeric(),
                TextInput::make('quantity')->integer()->integer(),
                Select::make('status')->options(ProductStatusEnum::labels())->default(ProductStatusEnum::Draft->value)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')->collection('images')->limit(1)->conversion('thumb')->label('image'),
                Tables\Columns\TextColumn::make('title')
                    ->words(10)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('usd') // أو أي عملة عندك
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->colors(ProductStatusEnum::colors())
                    ->sortable()->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')->options(ProductStatusEnum::labels()),
                SelectFilter::make('department_id')->relationship('department', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'images'=> Pages\ProductImages::route('/{record}/images'),
            'variation-types'=> Pages\ProductVariationTypes::route('/{record}/variation-types'),
        ];
    }
    // this func related to the media like images
    public static function getRecordSubNavigation(Page $page): array
    {
        return
            $page->generateNavigationItems(
                [
                    EditProduct::class,
                    ProductImages::class,
                    ProductVariationTypes::class
                ]
            );
    }
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user->hasRole(RolesEnum::Vendor);
    }
}
