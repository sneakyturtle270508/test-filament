<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutSessionResource\Pages;
use App\Models\Exercise;
use App\Models\WorkoutSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WorkoutSessionResource extends Resource
{
    protected static ?string $model = WorkoutSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Workout Name'),
                Forms\Components\DateTimePicker::make('started_at')
                    ->label('Started At')
                    ->default(now()),
                Forms\Components\Repeater::make('exerciseSets')
                    ->label('Exercises')
                    ->relationship('exerciseSets')
                    ->schema([
                        Forms\Components\Select::make('exercise_name')
                            ->required()
                            ->label('Exercise')
                            ->options(function () {
                                return Exercise::pluck('name', 'name')->toArray();
                            })
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Exercise Name'),
                                Forms\Components\Select::make('muscle_group')
                                    ->required()
                                    ->label('Muscle Group')
                                    ->options([
                                        'chest' => 'Chest',
                                        'back' => 'Back',
                                        'shoulders' => 'Shoulders',
                                        'biceps' => 'Biceps',
                                        'triceps' => 'Triceps',
                                        'legs' => 'Legs',
                                        'core' => 'Core',
                                        'full_body' => 'Full Body',
                                    ]),
                            ])
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('reps')
                            ->required()
                            ->numeric()
                            ->label('Reps')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->label('kg')
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Workout'),
                Tables\Columns\TextColumn::make('exerciseSets_count')
                    ->label('Sets')
                    ->counts('exerciseSets'),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->label('Date'),
            ])
            ->defaultSort('started_at', 'desc')
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
            'index' => Pages\ListWorkoutSessions::route('/'),
            'create' => Pages\CreateWorkoutSession::route('/create'),
            'edit' => Pages\EditWorkoutSession::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }
}
