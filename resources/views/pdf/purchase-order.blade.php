<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
            <style>
                .clearfix:after {
                    content: "";
                    display: table;
                    clear: both;
                }

                a {
                    color: #5D6975;
                    text-decoration: underline;
                }

                body {
                    position: relative;
                    /* width: 21cm;   */
                    /* height: 29.7cm;  */
                    margin: 0 auto; 
                    color: #001028;
                    background: #FFFFFF; 
                    font-family: Arial, sans-serif; 
                    font-size: 12px; 
                    font-family: Arial;
                }

                header {
                    padding: 10px 0;
                    margin-bottom: 30px;
                }

                #logo {
                    text-align: center;
                    margin-bottom: 10px;
                }

                #logo img {
                    width: 90px;
                }

                h1 {
                    border-top: 1px solid  #5D6975;
                    border-bottom: 1px solid  #5D6975;
                    color: #5D6975;
                    font-size: 2.4em;
                    line-height: 1.4em;
                    font-weight: normal;
                    text-align: center;
                    margin: 0 0 20px 0;
                    background: url(dimension.png);
                }

                #project {
                    float: left;
                }

                #project span {
                    color: #5D6975;
                    text-align: right;
                    width: 52px;
                    margin-right: 10px;
                    display: inline-block;
                    font-size: 0.8em;
                }

                #company {
                    float: right;
                    text-align: right;
                }

                #project div,
                #company div {
                    white-space: nowrap;        
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    border-spacing: 0;
                    margin-bottom: 20px;
                }

                /* table tr:nth-child(2n-1) td {
                    background: #F5F5F5;
                } */

                table th,
                table td {
                    text-align: center;
                }

                table th {
                    padding: 5px 20px;
                    color: #5D6975;
                    border-bottom: 1px solid #C1CED9;
                    white-space: nowrap;        
                    font-weight: normal;
                }

                table .service,
                table .desc {
                    text-align: left;
                }

                table td {
                    padding: 20px;
                    text-align: right;
                }

                table td.service,
                table td.desc {
                    vertical-align: top;
                }

                table td.unit,
                table td.qty,
                table td.total {
                    font-size: 1.2em;
                }

                table td.grand {
                    border-top: 1px solid #5D6975;;
                }

                #notices .notice {
                    color: #5D6975;
                    font-size: 1.2em;
                }

                footer {
                    color: #5D6975;
                    width: 100%;
                    height: 30px;
                    position: absolute;
                    bottom: 0;
                    border-top: 1px solid #C1CED9;
                    padding: 8px 0;
                    text-align: center;
                }

                .font-family-bold {
                    font-weight: bold;
                }

                .text-muted {
                    color: #5D6975; 
                }

                .fst-italic {
                    font-style: italic;
                }
            </style>
</head>

    <body>
        <header class="clearfix">
            <div id="logo">
                <img src="{{ url('/') }}/assets/images/logo.png">
            </div>

            <h1>{{ $purchaseOrder->id }}</h1>
            
            <div id="company" class="clearfix">
                <div>Beauty CLinic</div>
                <div>Jalan Kalimantan 1,<br /> Jawa Tengah 50519, ID</div>
                <div>(024) 671-5351</div>
                <div><a href="mailto:company@example.com">support@beauty-clinic.com</a></div>
            </div>

            <div id="project">
                <div><span>Tanggal</span> {{ \Carbon\Carbon::parse($purchaseOrder->created_at)->format('d/m/Y'); }}</div>
            </div>
        </header>

        <main>
            <table>
                <thead>
                    <tr>
                        <th class="service">Nama Barang</th>
                        <th class="desc">Supplier</th>
                        <th>QTY</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                    </tr>
                </thead>

                @foreach($details as $detail)
                <tbody>
                    <tr>
                        <td colspan="5" style="text-align:left">
                            <div class="font-family-bold">{{ $detail['id'] }}</div>
                            <div class="text-muted fst-italic">{{ $detail['note'] }}</div>
                        </td>
                    </tr>
                    @foreach($detail['items'] as $item)
                    <tr>
                        <td class="service">{{ $item['item'] }}</td>
                        <td class="desc">{{ $item['supplier'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['item_unit'] }}</td>
                        <td>@rupiah($item['price'])</td>
                    </tr>
                    @endforeach
                </tbody>
                @endforeach

                <tfoot>
                    <tr><td colspan="5"></td></tr>
                    <tr>
                        <td colspan="4"><div class="text-end font-family-bold">Total</div></td>
                        <td>@rupiah($purchaseOrder->total)</td>
                    </tr>
                </tfoot>
            </table>

            <div id="notices">
                <div>Note:</div>
                <div class="notice">{{ $purchaseOrder->note }}</div>
            </div>
        </main>
    </body>
</html>