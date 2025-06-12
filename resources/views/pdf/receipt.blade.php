<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7fa;
            position: relative;
        }

        #page {
            padding: 25px;
            background: #fff;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        /* Logo at the top */
        .logo {
            width: 100%;
            max-width: 150px;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        h2 {
            color: #f33a40;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items th,
        .items td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .items th {
            background-color: #f4f4f4;
        }

        .items tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items.last {
            margin-top: 30px;
        }

        .items td {
            font-size: 14px;
        }

        .items td:last-child {
            text-align: right;
        }

        .items th:last-child {
            text-align: right;
        }

        .items .last td {
            border-bottom: 0;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translate(-50%);
            font-size: 14px;
            text-align: center;
            width: 100%;
        }

        .footer i {
            color: #555;
        }
    </style>
</head>

<body>

    <div id="page">
        <!-- Logo at the top -->


        <div style="text-align: left; display: flex; justify-content: space-between; padding-right: 5px; padding-left: 5px">
            <h1 style="letter-spacing: 3px; margin: 0">INVOICE</h1>
            <div style="text-align: right; vertical-align: top; align-self: flex-start;">
                <p style="margin: 0; color: #555;">Your Astrology</p>
                <p style="margin: 0; color: #555;">{{ $astrologer->first_name }} {{ $astrologer->last_name }}</p>
                <p style="margin: 0; color: #555;">{{ $astrologer->email }}</p>
                <p style="margin: 0; color: #555;">{{ $astrologer->phone }}</p>
            </div>
        </div>

        <hr>
        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="vertical-align: top; padding: 0; width: 60%; border: 0;">
                        <div style="margin-top: 5px; text-align: left;">
                            <p style="margin-top:0;margin-bottom: 5px;color: #666;">Bill To:</p>
                            <h3 style="margin-bottom: 5px; margin-top: 5px;color: #f33a40">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p style="margin: 0; color: #555;">Email: {{ $user->email }}</p>
                            <p style="margin: 0; color: #555;">Phone: {{ $user->phone }}</p>
                        </div>
                    </td>
                    <td style="padding: 0; width: 40%; border: 0; vertical-align: top; text-align: right;">
                        <table class="items last" style="width: auto; display: inline-table;">
                            <tbody>
                                <tr>
                                    <th>Invoice</th>
                                    <th>#INV-{{ $scheduleCall->created_at->format('Ymd') }}-{{ $scheduleCall->id }}</th>
                                </tr>
                                <tr>
                                    <td>Issued Date:</td>
                                    <td>{{ $scheduleCall->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Balance:</td>
                                    <td>₹{{ $transaction->amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>

        <table class="items">
            <thead>
                <tr>
                    <th>Services</th>
                    <th>Call Type</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Consultation with {{ $astrologer->first_name }} {{ $astrologer->last_name }} @if($scheduleCall->agend) for {{ $scheduleCall->agend }} @else . @endif</td>
                    <td>{{ ucfirst($scheduleCall->call_type) }}</td>
                    <td>₹{{ $transaction->amount }}</td>
                    <td>₹{{ $transaction->amount }}</td>
                </tr>
            </tbody>
        </table>

        <br>

        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="padding: 0; width: 60%; border: 0; vertical-align: top;">
                        <!-- Left empty -->
                    </td>
                    <td style="padding: 0; width: 40%; border: 0; text-align: right;">
                        <table class="items last" style="width: auto; display: inline-table;">
                            <tbody>
                                <tr>
                                    <td>Subtotal:</td>
                                    <td>₹{{ $transaction->amount }}</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td>₹{{ $transaction->amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <i>Thank you for your business. We appreciate your trust in our astrology services.</i>
        </div>
    </div>

</body>

</html>