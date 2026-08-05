<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #333333;
            margin: 0;
            padding: 30px;
            font-size: 13px;
            line-height: 1.4;
        }
        .sheet {
            border: 1px solid #cccccc;
            padding: 35px 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .company-name { font-size: 30px; color: #1f2933; margin: 0 0 6px 0; font-weight: 400; }
        .company-details { font-size: 12px; color: #444444; line-height: 1.6; }
        .company-details a { color: #4a6fa5; text-decoration: none; }
        .invoice-title { font-size: 34px; color: #7f9cc4; text-align: right; letter-spacing: 2px; margin: 0; font-weight: bold; }

        .meta-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .meta-table td { padding: 3px 0; font-size: 12px; }
        .meta-label { text-align: right; padding-right: 10px; color: #333333; white-space: nowrap; }
        .meta-value { border: 1px solid #999999; padding: 3px 8px !important; width: 110px; text-align: left; }
        .meta-value.highlight { background-color: #ccd7e8; }

        /* Bill To */
        .section-bar {
            background-color: #4a5f8a;
            color: #ffffff;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        .bill-to-box { width: 45%; margin-top: 25px; margin-bottom: 20px; }
        .bill-to-content { padding: 8px 10px; font-size: 12px; color: #333333; line-height: 1.7; }

        /* Line items */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table thead th {
            background-color: #4a5f8a;
            color: #ffffff;
            font-size: 12px;
            padding: 8px 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .items-table th.col-desc, .items-table td.col-desc { text-align: left; }
        .items-table th.col-taxed, .items-table td.col-taxed { text-align: center; width: 70px; }
        .items-table th.col-amount, .items-table td.col-amount { text-align: right; width: 100px; }
        .items-table tbody td { padding: 6px 10px; font-size: 12px; border-bottom: 1px solid #e5e5e5; }
        .items-table tbody tr:nth-child(even) { background-color: #f2f2f2; }
        .items-table tbody tr.empty-row td { height: 22px; }

        /* Totals */
        .totals-wrap { width: 100%; margin-top: 10px; }
        .totals-table { width: 260px; float: right; border-collapse: collapse; }
        .totals-table td { padding: 4px 6px; font-size: 12px; }
        .totals-table td.label { text-align: right; color: #333333; }
        .totals-table td.value { text-align: right; width: 90px; }
        .totals-table td.value-box { border: 1px solid #999999; }
        .totals-table tr.total-row td { border-top: 2px solid #4a5f8a; font-weight: bold; font-size: 14px; padding-top: 8px; }
        .totals-table tr.total-row td.value { color: #1f2933; }

        /* Comments */
        .comments-box { width: 48%; margin-top: 10px; border: 1px solid #cccccc; }
        .comments-content { padding: 10px; font-size: 12px; color: #333333; }
        .comments-content ol { margin: 0; padding-left: 18px; }
        .comments-content li { margin-bottom: 4px; }

        .clearfix::after { content: ""; display: table; clear: both; }

        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #333333;
            line-height: 1.8;
        }
        .footer-thanks { font-weight: bold; font-style: italic; font-size: 13px; margin-top: 4px; }

        .page-footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #999999;
        }
    </style>
</head>
<body>
<div class="sheet">

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <p class="company-name">{{ $company->name ?? 'Ashahi Phone Shop' }}</p>
                <div class="company-details">
                    {{ $company->address ?? '[Tomnubkobsrov street, Shan por Senchey, Phnom Penh]' }}<br>
                    {{ $company->city_state_zip ?? '[Phnom Penh, ST ZIP]' }}<br>
                    Phone: {{ $company->phone ?? '[096 7177 746]' }}<br>
                    Fax: {{ $company->fax ?? '[123456789]' }}<br>
                    Website: <a href="{{ $company->website ?? '#' }}">{{ $company->website ?? 'ashahi.com' }}</a>
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <p class="invoice-title">INVOICE</p>
                <table class="meta-table">
                    <tr>
    <td class="meta-label">DATE</td>
    <td class="meta-value">{{ $invoice->date ? $invoice->date->format('n/j/Y') : now()->format('n/j/Y') }}</td>
</tr>
<tr>
    <td class="meta-label">INVOICE #</td>
    <td class="meta-value">{{ $invoice->invoice_number }}</td>
</tr>
<tr>
    <td class="meta-label">CUSTOMER ID</td>
    <td class="meta-value">{{ $invoice->order->user->id }}</td>
</tr>
<tr>
    <td class="meta-label">DUE DATE</td>
    <td class="meta-value highlight">{{ $invoice->due_date ? $invoice->due_date->format('n/j/Y') : now()->addDays(30)->format('n/j/Y') }}</td>
</tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Bill To --}}
    <div class="bill-to-box">
        <div class="section-bar">BILL TO</div>
        <div class="bill-to-content">
            {{ $invoice->order->user->name }}<br>
            {{ $invoice->order->user->company_name ?? '' }}<br>
            {{ $invoice->order->user->address ?? '[Street Address]' }}<br>
            {{ $invoice->order->user->city_state_zip ?? '[City, ST ZIP]' }}<br>
            {{ $invoice->order->user->phone ?? '[Phone]' }}
        </div>
    </div>

    {{-- Line Items --}}
    <table class="items-table">
        <thead>
        <tr>
            <th class="col-desc">DESCRIPTION</th>
            <th class="col-taxed">TAXED</th>
            <th class="col-amount">AMOUNT</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->order->items as $item)
            <tr>
                <td class="col-desc">{{ $item->product_name }} &mdash; Qty: {{ $item->quantity }} @ ${{ number_format($item->unit_price, 2) }}</td>
                <td class="col-taxed">{{ $item->taxed ?? false ? 'X' : '' }}</td>
                <td class="col-amount">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        {{-- filler rows to mimic the template's blank lines --}}
        @for ($i = 0; $i < max(0, 6 - count($invoice->order->items)); $i++)
            <tr class="empty-row"><td class="col-desc">&nbsp;</td><td class="col-taxed"></td><td class="col-amount"></td></tr>
        @endfor
        </tbody>
    </table>

    {{-- Totals + Comments --}}
    <div class="clearfix" style="margin-top: 15px;">
        <div class="comments-box" style="float: left;">
            <div class="section-bar">OTHER COMMENTS</div>
            <div class="comments-content">
                <ol>
                    <li>Total payment due in 30 days</li>
                    <li>Please include the invoice number on your check</li>
                </ol>
            </div>
        </div>

        <table class="totals-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="value">${{ number_format($invoice->order->subtotal ?? $invoice->order->total, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Taxable</td>
                <td class="value">${{ number_format($invoice->order->taxable_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Tax rate</td>
                <td class="value value-box">{{ number_format($invoice->order->tax_rate ?? 0, 3) }}%</td>
            </tr>
            <tr>
                <td class="label">Tax due</td>
                <td class="value value-box">${{ number_format($invoice->order->tax_due ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Other</td>
                <td class="value value-box">&mdash;</td>
            </tr>
            <tr class="total-row">
                <td class="label">TOTAL</td>
                <td class="value">${{ number_format($invoice->order->total, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer-note">
        If you have any questions about this invoice, please contact<br>
        {{ $company->contact_name ?? '[Ashahi]' }}, {{ $company->phone ?? '[0967177746]' }}, {{ $company->email ?? '[nornchhayna01@gmail.com]' }}
        <div class="footer-thanks">Thank You For Your Business!</div>
    </div>

    <div class="page-footer">
        <span>{{ $company->website ?? '' }}</span>
        <span>Invoice #{{ $invoice->invoice_number }}</span>
    </div>

</div>
</body>
</html>