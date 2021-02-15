<body>
    <div class="clearfix">
        <div class="logo">
        </div>
        <h1>{{config('app.name')}}</h1>
        <div class="clearfix">
            <div class="module">
                <div><span>MODULE</span> {{modelToTitle($modelName)}}</div>
                <div><span>DATE</span> {{now()->format('D, d M Y')}}</div>
            </div>
        </div>
    </div>
    <div class="page">
        <table>
            <thead>
                <tr>
                    @foreach ($header as $key => $head)
                        @php
                            $rule[$key] = @$head[1];
                        @endphp
                    <th class="{{@$head[1]}}">{{$head[0]}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                    @foreach ($data as $key => $value)
                <tr>
                        @foreach ($columns as $kc => $column)
                    <td class="{{$rule[$kc]}}">{{$value[$column]}}</td>
                        @endforeach
                </tr>
                    @endforeach
            </tbody>
        </table>
    </div>

    {{-- Here's the magic. This MUST be inside body tag. Page count / total, centered at bottom of page --}}
    <script type="text/php">
        if (isset($pdf)) { 
            $text  = "page {PAGE_NUM} of {PAGE_COUNT}"; 
            $size  = 10; 
            $font  = $fontMetrics->getFont("Verdana"); 
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2; 
            {{-- $x = ($pdf->get_width() - $width) / 2; --}} 
            $x = ($pdf->get_width() - $width); 
            $y = $pdf->get_height() - 35; 
            $pdf->page_text($x, $y, $text, $font, $size, [0,0,0,0.5]); 
        }
    </script>
</body>
<style type="text/css">
.clearfix:after {
    content: "";
    display: table;
    clear: both;
}

.page {
    page-break-before: always;
    width: 100%;
}

.page:first-child {
    page-break-before: avoid;
}

a {
    color: #5D6975;
    text-decoration: underline;
}

@page {
    margin: 2%;
}

body {
    position: relative;
    /*width: 21cm;  */
    /*height: 29.7cm; */
    margin: 2%;
    color: #001028;
    background: #FFFFFF;
    font-family: Arial, sans-serif;
    font-size: 12px;
    font-family: Arial;
}

.logo {
    text-align: center;
    margin-bottom: 10px;
}

.logo img {
    height: 70px;
}

h1 {
    border-top: 1px solid #ff9d76;
    border-bottom: 1px solid #ff9d76;
    color: #ed6663;
    font-size: 17pt;
    line-height: 37px;
    text-align: center;
    font-variant: petite-caps;
    font-family: sans-serif;
}

.module {
    width: fit-content;
    font-variant: petite-caps;
    font-weight: 900;
    color: #ed6663;
    border-bottom: 1px solid #fbd9cc;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.module span {
    color: #f5a483;
    text-align: right;
    width: 52px;
    margin-right: 10px;
    display: inline-block;
    font-size: 8pt;
    font-weight: 700;
}

.module div{
    white-space: nowrap;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 10pt;
    color: #ed6663;
}

table tr:nth-child(2n-1) td {
    background: #ffdbc5;
    color: #fe346e;
}

table th,
table td {
    text-align: center;
}

table th {
    padding: 5px 20px;
    color: #ed6663;
    border-bottom: 1px solid #C1CED9;
    white-space: nowrap;
    font-weight: normal;
    font-variant: petite-caps;
}

.text{
    text-align: left;
}

.number{
    text-align: right;
}

table td {
    padding: 20px;
    text-align: center;
}

table td.text{
    vertical-align: top;
}

table td.number{
    font-size: 11pt;
}
</style>