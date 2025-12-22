<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Admin Name</th>
            <th>Role</th>
            <th>Activity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $key => $activity)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $activity?->admin?->full_name }}</td>
                <td>{{ $activity?->admin?->role_name }}</td>
                <td>{{ print_r($activity?->activity, 1) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
