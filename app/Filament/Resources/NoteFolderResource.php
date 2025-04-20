<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteFolderResource\Pages;
use App\Filament\Resources\NoteFolderResource\RelationManagers;
use App\Models\NoteFolder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NoteFolderResource extends Resource
{
    protected static ?string $model = NoteFolder::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = '筆記資料夾';
    protected static ?string $modelLabel = '筆記資料夾';
    protected static ?string $pluralModelLabel = '筆記資料夾';
    protected static ?string $navigationGroup = '系統管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'name')
                    ->label('會員')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('parent_id', null)),
                Forms\Components\Select::make('parent_id')
                    ->relationship(
                        'parent',
                        'name',
                        fn (Builder $query, Forms\Get $get) => $query->where('member_id', $get('member_id'))
                    )
                    ->label('父層資料夾')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->allowHtml()
                    ->selectablePlaceholder(false),
                Forms\Components\TextInput::make('name')
                    ->label('名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('描述')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('啟用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('編號')
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('會員')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('arrow_path')
                    ->label('名稱')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('啟用'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編輯'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('批次刪除'),
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
            'index' => Pages\ListNoteFolders::route('/'),
            'create' => Pages\CreateNoteFolder::route('/create'),
            'edit' => Pages\EditNoteFolder::route('/{record}/edit'),
        ];
    }
}
