import { usePage } from '@inertiajs/vue3';

export function useStorage() {
  const page = usePage();
  const storageUrl = (path) => {
    if (!path) return null;
    const base = page.props.storageUrl ?? '/storage';
    return `${base}/${path}`;
  };
  return { storageUrl };
}
