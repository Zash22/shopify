

            @section('scripts')
                <script type="text/javascript">
                    {{--window.top.location.href = '{{ $installUrl }}';--}}

                            $( document ).ready(function() {
                        console.log( "ready!" );
                        window.top.location.href = '{!! $permission_url !!}';

                    });
                </script>
            @stop





