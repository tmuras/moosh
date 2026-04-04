import { useMemo } from 'react';
import { Link, useLocation } from 'react-router-dom';
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarGroupContent,
  SidebarMenu,
  SidebarMenuItem,
  SidebarMenuButton,
  SidebarHeader,
} from '@/components/ui/sidebar';
import { Terminal, BookOpen, Layers, Settings, FileOutput, Home } from 'lucide-react';
import { commands } from '@/data/commands';

const navItems = [
  { to: '/', label: 'Home', icon: Home },
  { to: '/getting-started', label: 'Getting Started', icon: BookOpen },
  { to: '/architecture', label: 'Architecture', icon: Layers },
  { to: '/global-options', label: 'Global Options', icon: Settings },
  { to: '/output-formats', label: 'Output Formats', icon: FileOutput },
  { to: '/commands', label: 'All Commands', icon: Terminal },
];

export function AppSidebar() {
  const location = useLocation();

  const sortedCommands = useMemo(
    () => [...commands].sort((a, b) => a.name.localeCompare(b.name)),
    [],
  );

  return (
    <Sidebar>
      <SidebarHeader className="border-b px-4 py-3">
        <Link to="/" className="flex items-center gap-2 font-semibold text-lg">
          <Terminal className="h-5 w-5" />
          <span>moosh2</span>
        </Link>
        <p className="text-xs text-muted-foreground mt-1">Moodle Shell Documentation</p>
      </SidebarHeader>
      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>Documentation</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {navItems.map((item) => (
                <SidebarMenuItem key={item.to}>
                  <SidebarMenuButton
                    render={<Link to={item.to} />}
                    isActive={location.pathname === item.to}
                  >
                    <item.icon className="h-4 w-4" />
                    <span>{item.label}</span>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              ))}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>

        <SidebarGroup>
          <SidebarGroupLabel>Command Reference</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {sortedCommands.map((cmd) => (
                <SidebarMenuItem key={cmd.name}>
                  <SidebarMenuButton
                    render={<Link to={`/commands/${cmd.category}/${cmd.name.split(':')[1]}`} />}
                    isActive={location.pathname === `/commands/${cmd.category}/${cmd.name.split(':')[1]}`}
                    size="sm"
                  >
                    <span className="font-mono text-xs">{cmd.name}</span>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              ))}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
    </Sidebar>
  );
}
