<label class="if-field {{ $full ?? false ? 'if-full' : '' }}" for="{{ $name }}"><span>{{ $label }}@if($required ?? false)<span aria-hidden="true"> *</span>@endif</span>
@if(isset($options))
<select id="{{ $name }}" name="{{ $name }}" @required($required ?? false)>@foreach($options as $key=>$option)<option value="{{ $key }}" @selected((string) old($name, $value ?? '') === (string) $key)>{{ $option }}</option>@endforeach</select>
@elseif(($type ?? '') === 'textarea')
<textarea id="{{ $name }}" name="{{ $name }}" rows="4" maxlength="3000">{{ old($name, $value ?? '') }}</textarea>
@else
<input id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ old($name, $value ?? '') }}" @required($required ?? false) @if(isset($step)) step="{{ $step }}" min="0" @endif @if(!in_array($type ?? 'text', ['datetime-local', 'date', 'number'])) maxlength="200" @endif>
@endif
</label>
