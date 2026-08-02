/**
 * Lấy URL gốc của Laravel API (Render.com).
 * - Khi chạy trên Netlify: dùng biến môi trường VITE_API_URL
 * - Khi chạy local: dùng đường dẫn tương đối '/' (Laravel local)
 */
export const API_BASE = import.meta.env.VITE_API_URL || '';

export const apiFetch = (path: string, options?: RequestInit) =>
    fetch(`${API_BASE}${path}`, options);
