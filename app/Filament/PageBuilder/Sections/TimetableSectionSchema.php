<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use App\Filament\Components\ButtonInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class TimetableSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'timetable';
    }

    public static function getLabel(): string
    {
        return __('section-timetable.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::CalendarDays;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('section-timetable.section_general'))
                ->columnSpanFull()
                ->schema([
                    TextInput::make('general.title')
                        ->label(__('section-timetable.general_title'))
                        ->required(),

                    RichEditor::make('general.text')
                        ->label(__('section-timetable.general_text'))
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                        ->columnSpanFull(),
                ]),

            Section::make(__('section-timetable.section_signup'))
                ->columnSpanFull()
                ->schema([
                    TextInput::make('signup.title')
                        ->label(__('section-timetable.signup_title'))
                        ->required(),

                    RichEditor::make('signup.text')
                        ->label(__('section-timetable.signup_text'))
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                        ->columnSpanFull(),

                    ButtonInput::make('signup.button', __('section-timetable.signup_button'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('section-timetable.section_timetables'))
                ->columnSpanFull()
                ->schema([
                    TextInput::make('timetable_selector.title')
                        ->label(__('section-timetable.timetables_title'))
                        ->required(),

                    TextInput::make('timetable_selector.detail')
                        ->label(__('section-timetable.timetables_detail')),

                    Repeater::make('timetable_selector.timetables')
                        ->label(__('section-timetable.timetables'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('section-timetable.timetable_name'))
                                ->required(),

                            FileUpload::make('img')
                                ->label(__('section-timetable.timetable_img'))
                                ->image()
                                ->visibility('public')
                                ->directory('timetables'),

                            FileUpload::make('pdf')
                                ->label(__('section-timetable.timetable_pdf'))
                                ->acceptedFileTypes(['application/pdf'])
                                ->visibility('public')
                                ->directory('timetables'),
                        ])
                        ->reorderable()
                        ->columnSpanFull(),
                ]),
        ];
    }
}
