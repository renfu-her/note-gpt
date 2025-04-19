<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Filament\Resources\NoteResource\RelationManagers;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = '筆記管理';
    protected static ?string $modelLabel = '筆記';
    protected static ?string $pluralModelLabel = '筆記';
    protected static ?string $navigationGroup = '系統管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'name')
                    ->label('會員')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('標題')
                    ->required()
                    ->maxLength(255),
                Forms\Components\MarkdownEditor::make('content')
                    ->label('內容')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('啟用')
                    ->inline(false)
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('編號')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('標題')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('member.name')->label('會員')->sortable()->searchable(),
                Tables\Columns\BooleanColumn::make('is_active')->label('啟用'),
                Tables\Columns\TextColumn::make('created_at')->label('建立時間')->dateTime(),
            ])
            ->filters([
                // 依需求新增過濾器
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
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }
}
