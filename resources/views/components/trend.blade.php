@props(['data', 'label' => 'vs prev'])
@php $up = ($data['direction'] ?? 'up') === 'up'; @endphp
<span class="trend {{ $up ? 'up' : 'down' }}" style="display:inline-flex;align-items:center;gap:3px">
    <x-icon :name="$up ? 'up' : 'down'" size="13" />{{ $data['percent'] ?? 0 }}% {{ $label }}
</span>
