#!/usr/bin/env python3

from __future__ import annotations

import argparse
import json
import sys

from bakong_khqr import KHQR


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generate a Bakong KHQR payload")
    parser.add_argument("--bank-account", required=True)
    parser.add_argument("--merchant-name", required=True)
    parser.add_argument("--merchant-city", required=True)
    parser.add_argument("--amount", required=True, type=float)
    parser.add_argument("--currency", default="USD")
    parser.add_argument("--store-label", default="KKS Store")
    parser.add_argument("--phone-number", default="")
    parser.add_argument("--bill-number", default="")
    parser.add_argument("--terminal-label", default="Online")
    parser.add_argument("--expiration", default=1, type=int)
    return parser.parse_args()


def main() -> int:
    args = parse_args()

    khqr = KHQR()
    qr = khqr.create_qr(
        bank_account=args.bank_account,
        merchant_name=args.merchant_name,
        merchant_city=args.merchant_city,
        amount=args.amount,
        currency=args.currency,
        store_label=args.store_label,
        phone_number=args.phone_number,
        bill_number=args.bill_number,
        terminal_label=args.terminal_label,
        static=False,
        expiration=args.expiration,
    )

    print(json.dumps({
        "qr": qr,
        "md5": khqr.generate_md5(qr),
        "bill_number": args.bill_number,
    }, ensure_ascii=True))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
