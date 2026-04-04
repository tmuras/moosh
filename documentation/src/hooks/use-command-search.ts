import { useMemo } from 'react';
import { commands } from '@/data/commands';
import type { Command } from '@/data/types';

export function useCommandSearch(query: string): Command[] {
  return useMemo(() => {
    if (!query.trim()) return commands;
    const lower = query.toLowerCase();
    return commands.filter(
      (cmd) =>
        cmd.name.toLowerCase().includes(lower) ||
        cmd.description.toLowerCase().includes(lower) ||
        cmd.category.toLowerCase().includes(lower)
    );
  }, [query]);
}
