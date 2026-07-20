export function initials(name) {
    if (!name) return '?';

    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0].toUpperCase())
        .join('');
}

export function formatDateTime(value) {
    if (!value) return 'Дата не указана';

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

export function formatFormat(value) {
    if (!value) return 'TBD';

    return `Bo${value}`;
}

export function capitalize(value) {
    if (!value) return value;

    return value[0].toUpperCase() + value.slice(1);
}
