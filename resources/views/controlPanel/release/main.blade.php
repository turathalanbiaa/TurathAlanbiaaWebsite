@extends("controlPanel.layout.mainLayout")

@section("title")
    <title>الاصدارات</title>
@endsection

@section("content")
    <div class="ui segment">
        <div class="ui grid">
            <div class="thirteen wide computer thirteen wide tablet sixteen wide mobile column">
                <form class="ui large form" method="get" action="/ControlPanel/releases" dir="rtl">
                    <div class="sixteen wide field">
                        <div class="ui left icon input">
                            <input type="text" placeholder="بحث عن اصدار... " name="query" style="text-align: right;">
                            <i class="search icon"></i>
                        </div>
                    </div>
                </form>
            </div>

            <div class="three wide computer three wide tablet sixteen wide mobile column">
                <a class="ui fluid positive large button" href="/ControlPanel/release/create">
                    <span>أضافة</span>
                </a>
            </div>

            <div class="sixteen wide column">
                <table class="ui celled large unstackable table">
                    <thead>
                    <tr>
                        <th class="center aligned">رقم</th>
                        <th class="right aligned">عنوان الأصدار</th>
                        <th class="center aligned">تاريخ</th>
                        <th class="center aligned">خيارات</th>
                    </tr>
                    </thead>

                    <tbody>
                        @if(count($releases) > 0)
                            @foreach($releases as $release)
                                <tr>
                                    <td class="center aligned">{{$release->id}}</td>
                                    <td class="right aligned">{{$release->title}}</td>
                                    <td class="center aligned">{{$release->date}}</td>
                                    <td class="center aligned">
                                        <button class="ui fluid red button" data-action="delete" data-id="{{$release->id}}">حذف</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">
                                    <div class="ui center aligned header">
                                        <div class="ui hidden divider"></div>
                                        <div class="ui hidden divider"></div>
                                        <div class="ui hidden divider"></div>
                                        <span>لا توجد نتائج</span>
                                        <div class="ui hidden divider"></div>
                                        <div class="ui hidden divider"></div>
                                        <div class="ui hidden divider"></div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if($releases->hasPages())
                <div class="sixteen wide teal column">
                    @if(isset($_GET["query"]))
                        {{$releases->appends(['query' => $_GET["query"]])->links()}}
                    @else
                        {{$releases->links()}}
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@section("extra-content")
    <div class="ui mini modal">
        <h3 class="ui center aligned top attached grey inverted header">
            <span>هل انت متأكد من حذف هذا الاصدار !!!</span>
        </h3>
        <div class="content">
            <div class="ui hidden divider"></div>

            <h3 class="ui center aligned header">
                <span>رقم الاصدار هو - </span>
                <span id="release-number"></span>
            </h3>

            <div class="ui divider"></div>

            <div class="actions" style="text-align: center;">
                <button class="ui positive button">نعم</button>
                <button class="ui negative button">لا</button>
            </div>

            <div class="ui hidden divider"></div>
        </div>
    </div>
@endsection

@section("script")
    <script>
        $(document).ready(function () {
            var pagination = $(".pagination");
            pagination.removeClass("pagination").addClass("ui right aligned pagination teal menu");
            pagination.css("padding","0");
            pagination.find('li').addClass('item');
        });
        $("button[data-action='delete']").click(function () {
            $("#release-number").html($(this).data("id"));
            $(".modal")
                .modal({
                    'closable' : false,
                    'transition': 'horizontal flip'
                })
                .modal("show");
        });
        $("button.positive.button").click(function () {
            var id = $("#release-number").html();
            var _token = "{{ csrf_token() }}";
            $.ajax({
                type: "POST",
                url: "/ControlPanel/release/delete",
                data: {_token:_token, id:id},
                datatype: 'json',
                success: function(result) {
                    if (result["notFound"] == true)
                    {
                        snackbar("هذا الاصدار غير موجود." , 3000 , "warning");
                    }

                    if (result["success"] == false)
                    {
                        snackbar("لم يتم حذف الاصدار,يرجى اعدة المحاولة." , 3000 , "error");
                    }
                    else if (result["success"] == true)
                    {
                        snackbar("تم حذف الاصدار." , 3000 , "success");
                    }
                },
                error: function() {
                    snackbar("تحقق من الاتصال بالانترنت" , 3000 , "error");
                } ,
                complete : function() {

                }
            });
        });
    </script>
@endsection