<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Total</th>
            <th>Satuan</th>
            <th>Tgl. Dibuat</th>
            <th>Tgl. Terakhir Diubah</th>
        </tr>
    </thead>

    <tbody>
        @php $i = 0; @endphp
        @foreach ($data as $stock)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $stock->item->name ?? '' }}</td>
            <td>{{ $stock->total ?? 0 }}</td>
            <td>{{ $stock->item->unit->name ?? '' }}</td>
            <td>{{ \Carbon\Carbon::parse($stock->created_at)->format('Y-m-d H:i:s'); }}</td>
            <td>{{ \Carbon\Carbon::parse($stock->updated_at)->format('Y-m-d H:i:s'); }}</td>
        </tr>
        @php $i++; @endphp
        @endforeach
    </tbody>
</table>