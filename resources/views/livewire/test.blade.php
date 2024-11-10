<div>
    <form wire:submit="fping">
        <input type="text" wire:model="ipAddress">
        <button type="submit">Ping</button>
    </form>

    <div class="mt-5">
        @if(empty($response) === false)
            <pre>{{ $response }}</pre>
        @endif
    </div>
</div>
