import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';

const bootstrapLevels = [
  { name: 'None', value: 0, description: 'No Moodle includes at all. Used for commands that work with backup files or external APIs.' },
  { name: 'Config', value: 1, description: 'Loads config.php only (sets ABORT_AFTER_CONFIG). Provides database connection details without full Moodle bootstrap.' },
  { name: 'Full', value: 2, description: 'Standard full bootstrap with CLI_SCRIPT defined. Most commands use this level.' },
  { name: 'FullNoCli', value: 3, description: 'Browser-context bootstrap without CLI_SCRIPT. Sets $_SERVER globals to simulate a web request. Used for admin login.' },
  { name: 'DbOnly', value: 4, description: 'Minimal bootstrap loading only database libraries and calling setup_DB(). For direct database operations.' },
  { name: 'FullNoAdminCheck', value: 5, description: 'Full bootstrap without requiring admin user login. Default for BaseCommand.' },
];

export function ArchitecturePage() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Architecture</h1>
        <p className="text-muted-foreground mt-2">
          How moosh2 bootstraps Moodle and dispatches commands to version-specific handlers.
        </p>
      </div>

      <section className="space-y-4">
        <h2 className="text-xl font-semibold">Command Pattern</h2>
        <p className="text-muted-foreground">
          Every moosh2 command follows a three-part structure:
        </p>
        <div className="grid gap-4 sm:grid-cols-3">
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-base">Command Class</CardTitle>
            </CardHeader>
            <CardContent>
              <CardDescription>
                Extends <code className="bg-muted px-1 rounded text-xs">BaseCommand</code>. Sets the command name,
                description, and bootstrap level. Delegates to a handler.
              </CardDescription>
            </CardContent>
          </Card>
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-base">Handler Class</CardTitle>
            </CardHeader>
            <CardContent>
              <CardDescription>
                Extends <code className="bg-muted px-1 rounded text-xs">BaseHandler</code>. Implements version-specific
                logic in <code className="bg-muted px-1 rounded text-xs">configureCommand()</code> and{' '}
                <code className="bg-muted px-1 rounded text-xs">handle()</code>.
              </CardDescription>
            </CardContent>
          </Card>
          <Card>
            <CardHeader className="pb-2">
              <CardTitle className="text-base">Helper Traits</CardTitle>
            </CardHeader>
            <CardContent>
              <CardDescription>
                Optional traits for shared logic like query building, filtering, and formatting
                across handler versions.
              </CardDescription>
            </CardContent>
          </Card>
        </div>
      </section>

      <section className="space-y-4">
        <h2 className="text-xl font-semibold">Version-Specific Dispatch</h2>
        <p className="text-muted-foreground">
          moosh2 detects the Moodle version early (in the Application constructor) by parsing{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">version.php</code>. Commands select the appropriate
          handler based on the detected version using{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">MoodleVersion::isAtLeast()</code>.
        </p>
        <p className="text-muted-foreground">
          Handler naming convention:{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">{'CommandName{MajorMinor}Handler.php'}</code>{' '}
          (e.g., <code className="bg-muted px-1.5 py-0.5 rounded text-sm">CourseList52Handler.php</code> for Moodle 5.2+).
        </p>
        <p className="text-muted-foreground">
          The <code className="bg-muted px-1.5 py-0.5 rounded text-sm">#[SinceVersion]</code> PHP attribute can be
          applied to command classes to gate them to a minimum Moodle version. Commands will show an
          error if the running Moodle is below the specified version.
        </p>
      </section>

      <section className="space-y-4">
        <h2 className="text-xl font-semibold">Bootstrap Levels</h2>
        <p className="text-muted-foreground">
          Commands declare a bootstrap level controlling how deeply Moodle is initialized before
          the command runs. Handlers can override the command's bootstrap level.
        </p>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Level</TableHead>
              <TableHead>Value</TableHead>
              <TableHead>Description</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {bootstrapLevels.map((level) => (
              <TableRow key={level.name}>
                <TableCell>
                  <Badge variant="outline">{level.name}</Badge>
                </TableCell>
                <TableCell className="font-mono">{level.value}</TableCell>
                <TableCell className="text-muted-foreground">{level.description}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </section>

      <section className="space-y-4">
        <h2 className="text-xl font-semibold">Execution Flow</h2>
        <ol className="list-decimal list-inside space-y-2 text-muted-foreground">
          <li>Application resolves Moodle path (from <code className="bg-muted px-1 rounded text-xs">--moodle-path</code> or by walking up directories)</li>
          <li>Application parses <code className="bg-muted px-1 rounded text-xs">version.php</code> to detect Moodle version</li>
          <li>Command is matched and its handler selected based on version</li>
          <li>Handler's <code className="bg-muted px-1 rounded text-xs">configureCommand()</code> registers arguments and options</li>
          <li><code className="bg-muted px-1 rounded text-xs">#[SinceVersion]</code> attribute is checked against running Moodle</li>
          <li>Moodle is bootstrapped to the effective bootstrap level</li>
          <li>Handler's <code className="bg-muted px-1 rounded text-xs">handle()</code> method executes the command logic</li>
        </ol>
      </section>
    </div>
  );
}
