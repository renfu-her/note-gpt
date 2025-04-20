<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class NoteRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';
    protected static ?string $title = '筆記';
    protected static ?string $recordTitleAttribute = 'title';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('編號')->sortable(),
                Tables\Columns\TextColumn::make('folder.arrow_path')
                    ->label('資料夾')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?? '-'),
                Tables\Columns\TextColumn::make('title')->label('標題')->sortable()->searchable(),
                Tables\Columns\BooleanColumn::make('is_active')->label('啟用'),
                Tables\Columns\TextColumn::make('created_at')->label('建立時間')->dateTime(),
            ])
            ->filters([
                // 可依需求新增過濾器
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('新增筆記'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編輯'),
                Tables\Actions\ViewAction::make()->label('查看'),
                Tables\Actions\DeleteAction::make()->label('刪除'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('批次刪除'),
                ]),
            ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('folder_id')
                    ->relationship('folder', 'name')
                    ->label('資料夾')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->allowHtml()
                    ->selectablePlaceholder(false),
                Forms\Components\TextInput::make('title')
                    ->label('標題')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\MarkdownEditor::make('content')
                    ->label('內容')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->label('啟用')->default(true),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('title')->label('標題'),
                TextEntry::make('content')->label('內容')->markdown(),
                IconEntry::make('is_active')
                    ->label('啟用')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime(),
            ]);
    }
} 