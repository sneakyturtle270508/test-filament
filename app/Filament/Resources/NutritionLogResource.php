<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NutritionLogResource\Pages;
use App\Models\NutritionLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NutritionLogResource extends Resource
{
    protected static ?string $model = NutritionLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Date')
                    ->default(today()),
                Forms\Components\TextInput::make('calories')
                    ->numeric()
                    ->label('Calories'),
                Forms\Components\TextInput::make('protein')
                    ->numeric()
                    ->label('Protein (g)'),
                Forms\Components\TextInput::make('carbs')
                    ->numeric()
                    ->label('Carbs (g)'),
                Forms\Components\TextInput::make('fat')
                    ->numeric()
                    ->label('Fat (g)'),
                Forms\Components\Select::make('stress_level')
                    ->label('Stress Level')
                    ->options([
                        1 => '1 - Very Low',
                        2 => '2 - Low',
                        3 => '3 - Fairly Low',
                        4 => '4 - Below Average',
                        5 => '5 - Average',
                        6 => '6 - Above Average',
                        7 => '7 - High',
                        8 => '8 - Very High',
                        9 => '9 - Extremely High',
                        10 => '10 - Maximum',
                    ]),
            ]);
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date'),
                Tables\Columns\TextColumn::make('calories')
                    ->label('Calories'),
                Tables\Columns\TextColumn::make('protein')
                    ->label('Protein'),
                Tables\Columns\TextColumn::make('carbs')
                    ->label('Carbs'),
                Tables\Columns\TextColumn::make('fat')
                    ->label('Fat'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListNutritionLogs::route('/'),
            'create' => Pages\CreateNutritionLog::route('/create'),
            'edit' => Pages\EditNutritionLog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }
}
