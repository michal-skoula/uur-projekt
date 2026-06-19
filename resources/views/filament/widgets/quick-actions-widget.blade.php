<x-filament-widgets::widget>
    <div class="flex flex-wrap gap-3">
        @foreach ($this->getWidgetActions() as $action)
            {{ $action }}
        @endforeach
    </div>
</x-filament-widgets::widget>
