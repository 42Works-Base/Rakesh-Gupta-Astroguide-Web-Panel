<script>
	@if(Session::has('success'))
	toastr.success("{{ Session::get('success') }}");
	@endif
	@if(Session::has('error'))
	toastr.error("{{ Session::get('error') }}");
	@endif

	@if(Session::has('error_message_array'))
	toastr.error("{!! Session::get('error_message_array') !!}");
	@endif
</script>