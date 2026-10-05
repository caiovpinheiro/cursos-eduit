<div>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
    <input type="text" wire:model="count">
    <span>current time {{ time() }}</span>
    <button wire:click="$refresh">Refresh</button>
</div>
