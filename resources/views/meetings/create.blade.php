<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Schedule Meeting</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    @php
        // Only allow dates in the future
        $minDate = now()->format('Y-m-d\TH:i');
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show"
                        role="alert">
                        {{ session('success') }}
                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Error Message --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show"
                        role="alert">
                        {{ session('error') }}
                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Schedule Google Meet</h5>
                    </div>

                    <div class="card-body">

                        <form method="POST"
                            action="{{ route('meetings.store') }}">
                            @csrf

                            {{-- Hidden Timezone Input --}}
                            <input type="hidden"
                                name="timezone"
                                id="user_timezone">

                            {{-- Student --}}
                            <div class="mb-3">
                                <label class="form-label">Select Student</label>
                                <select name="student_id"
                                    class="form-select"
                                    required>
                                    <option value="">-- Choose Student --</option>
                                    @foreach ($students as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->name }} ({{ $s->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label">Meeting Title</label>
                                <input name="title"
                                    class="form-control"
                                    required
                                    placeholder="Enter meeting title">
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Optional meeting description"></textarea>
                            </div>

                            <div class="row">
                                {{-- Start Time --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Time (Future Only)</label>
                                    <input type="datetime-local"
                                        name="start_time"
                                        class="form-control"
                                        required
                                        min="{{ $minDate }}">
                                </div>

                                {{-- End Time --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Time (Future Only)</label>
                                    <input type="datetime-local"
                                        name="end_time"
                                        class="form-control"
                                        required
                                        min="{{ $minDate }}">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary px-4">
                                    Schedule Meeting
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

                <div class="card mt-4 shadow">
                    <div class="card-body">
                        <table class="table-bordered table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Start time</th>
                                    <th>Link</th>
                                    <th>Event Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($meetings as $meeting)
                                    <tr>
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($meeting->start_time)->format('Y-M-d h:i A') }}
                                        </td>
                                        <td><a href="{{ $meeting->meet_link }}">{{ $meeting->meet_link }}</a></td>
                                        <td>{{ $meeting->google_event_id }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-detect User Timezone -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.getElementById("user_timezone").value = timezone;
        });
    </script>

</body>

</html>
