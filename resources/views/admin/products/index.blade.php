<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>
    <h1>Products</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Category</th>
                <th>Color</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->address }}</td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->color->name }}</td>
                <td>{{ $product->address_status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 