@php
    use Knuckles\Scribe\Tools\WritingUtils as u;
    /** @var  Knuckles\Camel\Output\OutputEndpointData $endpoint */
@endphp
```bash
curl --request {{$endpoint->httpMethods[0]}} \
    {{$endpoint->httpMethods[0] == 'GET' ? '--get ' : ''}}"{{ rtrim($baseUrl, '/')}}/{{ ltrim($endpoint->boundUri, '/') }}@if(count($endpoint->cleanQueryParameters))?{!! u::printQueryParamsAsString($endpoint->cleanQueryParameters) !!}@endif"@if(count($endpoint->headers)) \
@foreach($endpoint->headers as $header => $value)
    --header "{{$header}}: {{ addslashes($value) }}"@if(! ($loop->last) || ($loop->last && count($endpoint->bodyParameters))) \
@endif
@endforeach
@endif
@if($endpoint->hasFiles())
@foreach($endpoint->cleanBodyParameters as $parameter => $value)
@foreach(u::getParameterNamesAndValuesForFormData($parameter, $value) as $key => $actualValue)
    --form "{!! "$key=".$actualValue !!}" \
@endforeach
@endforeach
@foreach($endpoint->fileParameters as $parameter => $value)
@foreach(u::getParameterNamesAndValuesForFormData($parameter, $value) as $key => $file)
    --form "{!! "$key=@".$file->path() !!}" @if(!($loop->parent->last))\
@endif
@endforeach
@endforeach
@elseif(count($endpoint->cleanBodyParameters))
@if ($endpoint->headers['Content-Type'] == 'application/x-www-form-urlencoded')
    --data '{!! http_build_query($endpoint->cleanBodyParameters, '', '&') !!}'
@else
    --data '{!! json_encode($endpoint->cleanBodyParameters, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}'
@endif
@endif

```
