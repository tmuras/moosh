import { HashRouter, Routes, Route } from 'react-router-dom';
import { TooltipProvider } from '@/components/ui/tooltip';
import { ThemeProvider } from '@/components/ThemeProvider';
import { Layout } from '@/components/layout/Layout';
import { HomePage } from '@/pages/HomePage';
import { GettingStartedPage } from '@/pages/GettingStartedPage';
import { ArchitecturePage } from '@/pages/ArchitecturePage';
import { GlobalOptionsPage } from '@/pages/GlobalOptionsPage';
import { OutputFormatsPage } from '@/pages/OutputFormatsPage';
import { CommandsPage } from '@/pages/CommandsPage';
import { CommandDetailPage } from '@/pages/CommandDetailPage';

export default function App() {
  return (
    <ThemeProvider>
      <TooltipProvider>
        <HashRouter>
          <Routes>
            <Route element={<Layout />}>
              <Route path="/" element={<HomePage />} />
              <Route path="/getting-started" element={<GettingStartedPage />} />
              <Route path="/architecture" element={<ArchitecturePage />} />
              <Route path="/global-options" element={<GlobalOptionsPage />} />
              <Route path="/output-formats" element={<OutputFormatsPage />} />
              <Route path="/commands" element={<CommandsPage />} />
              <Route path="/commands/:category/:command" element={<CommandDetailPage />} />
            </Route>
          </Routes>
        </HashRouter>
      </TooltipProvider>
    </ThemeProvider>
  );
}
