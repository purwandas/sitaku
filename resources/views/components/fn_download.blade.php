<button class="dropdown-item" onclick="download('{{$type}}Button','{{ route($url) }}')" id="{{ $type }}Button">
	<i class="{{ $icon }}" id="{{ $type }}ButtonIcon"></i> {{ 'Export '.strtoupper($type) }}
</button>

@push('additional-js')
<script type="text/javascript">
	function download(elementId, url) {
        var element = $("#"+elementId);
        var icon = $("#"+elementId+"Icon");
        if (element.attr('disabled') != 'disabled') {
            var thisClass = icon.attr('class');

            var filters = {};
            var filterForm = $('#filter_form{{$name}}').serializeArray();
            $.each(filterForm, function(key, val){
                if (val.name != "_token" && val.value != "") {
                    filters[val.name] = val.value;
                }
            });

            $.ajax({
                type: 'POST',
                url: url,
                data: filters,
                beforeSend: function()
                {
                    element.attr('disabled', 'disabled');
                    icon.attr('class', 'fa fa-spinner fa-spin');
                },
                success: function (data) {
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    swal({
	                    title: data.title,
	                    text: data.msg,
	                    type: data.type,
	                });
                },
                error: function(xhr, textStatus, errorThrown){
                    element.removeAttr('disabled');
                    icon.attr('class', thisClass);
                    swal({
	                	title: "Gagal melakukan request",
	                	text: "Silahkan hubungi admin",
	                	type: "error"
	                });
                }
            });
        }
    }
</script>
@endpush