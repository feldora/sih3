// Utility untuk localStorage dengan expiry
export function setWithExpiry(key, value, ttl) {
    const now = new Date();
    const item = { value, expiry: now.getTime() + ttl };
    localStorage.setItem(key, JSON.stringify(item));
}

export function getWithExpiry(key) {
    const itemStr = localStorage.getItem(key);
    if (!itemStr) return null;
    const item = JSON.parse(itemStr);
    if (new Date().getTime() > item.expiry) {
        localStorage.removeItem(key);
        return null;
    }
    return item.value;
}
