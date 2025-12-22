<table>
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Service</th>
            <th>Booking Date</th>
            <th>Total Price ($)</th>
            <th>Payment Status</th>
            <td>Transaction ID</td>
            <th>Approval Status</th>
            <th>Cancelled</th>
            <th>Used Referral Code</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td> {{ $booking['user']['first_name'] . ' ' .$booking['user']['last_name'] }}</td>
                <td>{{ $booking['service']['name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($booking['date'])->format('jS F Y') }}</td>
                <td>{{ $booking['total_price'] }}</td>
                <td>{{ $booking['payment_status'] == 1 ? 'Paid' : 'Unpaid' }}</td>
                <td>{{ $booking['transaction_id'] }}</td>
                <td>{{ $booking['is_approved'] == 0 ? 'Pending' : ($booking['is_approved'] == 1 ? 'Approved' : 'Rejected') }}</td>
                <td>{{ $booking['is_cancelled'] == 1 ? 'Yes' : 'No' }}</td>
                <td>{{ $booking['used_referral_code']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
