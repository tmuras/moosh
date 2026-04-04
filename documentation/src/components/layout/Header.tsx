import { Link } from 'react-router-dom';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { ThemeToggle } from '@/components/ThemeToggle';
import { ExternalLinkIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

export function Header() {
  return (
    <header className="flex h-14 shrink-0 items-center gap-2 border-b px-4">
      <SidebarTrigger className="-ml-1" />
      <Separator orientation="vertical" className="mr-2 h-4" />
      <div className="flex-1" />
      <nav className="flex items-center gap-1">
        <Button variant="ghost" size="sm" render={<Link to="/commands" />}>
          Commands
        </Button>
        <Button
          variant="ghost"
          size="icon"
          render={<a href="https://github.com/tmuras/moosh" target="_blank" rel="noopener noreferrer" aria-label="GitHub" />}
        >
          <ExternalLinkIcon className="h-5 w-5" />
        </Button>
        <ThemeToggle />
      </nav>
    </header>
  );
}
