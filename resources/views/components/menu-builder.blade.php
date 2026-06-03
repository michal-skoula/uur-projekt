<?php

use App\Settings\NavMenuSettings;
use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="grid grid-cols-2 gap-8">
    @assets
    @vite('resources/js/filament.js')
    @endassets

    @php
        $s = app(NavMenuSettings::class);
        $str = $s->structure;

    @endphp
    <livewire:collection-group heading="Test" :pages-tree="$s->structure" :items="\App\Models\Page::all()"/>

    <div class="bg-teal-500!">
        <ul id="builder-selector" builder-nested>
            <li>Item 1</li>
            <li>Item 2</li>
            <li>Item 3</li>
            <li>Item 4</li>
        </ul>
    </div>

    <div class="bg-pink-500!">
        <ul id="builder-tree" builder-nested>
            <li>Item 1</li>
            <ul builder-nested class="ml-4">
                <li>Nested item 1</li>
                <li>Nested item 2</li>
                <li>Nested item 3</li>
            </ul>
            <li>Item 2</li>
            <li>Item 3</li>
            <li>Item 4</li>
        </ul>
    </div>

    @script
    <script>
        const left = document.getElementById('builder-selector');
        const right = document.getElementById('builder-tree');
        const nestedSortables = document.querySelectorAll("[builder-nested]")

        new Sortable(left, {
            group: 'shared', // set both lists to same group
            animation: 150
        });

        new Sortable(right, {
            group: 'shared',
            animation: 150
        });

        for (let i = 0; i < nestedSortables.length; i++) {
            new Sortable(nestedSortables[i], {
                group: 'nested',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65
            });
        }
    </script>
    @endscript
    {{-- It is never too late to be what you might have been. - George Eliot --}}
</div>
