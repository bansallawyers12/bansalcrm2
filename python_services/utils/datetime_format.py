"""Timezone-aware datetime formatting for Laravel CRM display."""

from __future__ import annotations

from datetime import datetime
from typing import Optional
from zoneinfo import ZoneInfo

DEFAULT_TIMEZONE = 'Australia/Melbourne'


def format_laravel_datetime(iso_value: str, timezone: Optional[str] = None) -> str:
    """Format an ISO datetime string for Laravel-style display (d/m/Y h:i a)."""
    tz_name = (timezone or DEFAULT_TIMEZONE).strip() or DEFAULT_TIMEZONE
    try:
        dt = datetime.fromisoformat(str(iso_value).replace('Z', '+00:00'))
        if dt.tzinfo is None:
            dt = dt.replace(tzinfo=ZoneInfo('UTC'))
        local = dt.astimezone(ZoneInfo(tz_name))
        return local.strftime('%d/%m/%Y %I:%M %p').lower()
    except Exception:
        return str(iso_value)
