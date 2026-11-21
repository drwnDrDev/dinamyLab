@props(['colspan'])

<tr>
    <td colspan="{{ $colspan }}" class="!py-10 text-center text-sm text-muted dark:text-slate-400">{{ $slot }}</td>
</tr>
