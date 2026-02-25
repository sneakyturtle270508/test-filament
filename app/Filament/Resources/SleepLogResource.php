<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SleepLogResource\Pages;
use App\Models\SleepLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SleepLogResource extends Resource
{
    protected static ?string $model = SleepLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-moon';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Date')
                    ->default(today()),
                Forms\Components\TextInput::make('hours')
                    ->required()
                    ->numeric()
                    ->label('Hours of Sleep'),
                Forms\Components\Select::make('quality')
                    ->label('Sleep Quality')
                    ->options([
                        1 => '1 - Very Poor',
                        2 => '2 - Poor',
                        3 => '3 - Fair',
                        4 => '4 - Below Average',
                        5 => '5 - Average',
                        6 => '6 - Above Average',
                        7 => '7 - Good',
                        8 => '8 - Very Good',
                        9 => '9 - Excellent',
                        10 => '10 - Perfect',
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
                Tables\Columns\TextColumn::make('hours')
                    ->label('Hours'),
                Tables\Columns\TextColumn::make('quality')
                    ->label('Quality'),
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
            'index' => Pages\ListSleepLogs::route('/'),
            'create' => Pages\CreateSleepLog::route('/create'),
            'edit' => Pages\EditSleepLog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }
}
