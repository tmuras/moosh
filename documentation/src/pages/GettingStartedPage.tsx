import { CodeBlock } from '@/components/CodeBlock';

export function GettingStartedPage() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Getting Started</h1>
        <p className="text-muted-foreground mt-2">
          Install moosh2 and run your first command.
        </p>
      </div>

      <section className="space-y-3">
        <h2 className="text-xl font-semibold">Requirements</h2>
        <ul className="list-disc list-inside space-y-1 text-muted-foreground">
          <li>PHP 8.2 or higher</li>
          <li>Composer</li>
          <li>A working Moodle installation (5.1+)</li>
        </ul>
      </section>

      <section className="space-y-3">
        <h2 className="text-xl font-semibold">Installation</h2>
        <CodeBlock>{`git clone https://github.com/tmuras/moosh.git moosh2
cd moosh2
composer install`}</CodeBlock>
      </section>

      <section className="space-y-3">
        <h2 className="text-xl font-semibold">Basic Usage</h2>
        <p className="text-muted-foreground">
          moosh2 can be run using either entry point:
        </p>
        <CodeBlock>{`php moosh.php <command> [options] [arguments]
# or
php bin/moosh <command> [options] [arguments]`}</CodeBlock>
      </section>

      <section className="space-y-3">
        <h2 className="text-xl font-semibold">Moodle Path Detection</h2>
        <p className="text-muted-foreground">
          moosh2 automatically detects the Moodle installation by walking up the directory tree
          from the current working directory. It looks for <code className="bg-muted px-1.5 py-0.5 rounded text-sm">config.php</code>,{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">version.php</code>, and{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">lib/moodlelib.php</code> to
          identify the Moodle root.
        </p>
        <p className="text-muted-foreground">
          Alternatively, specify the path explicitly:
        </p>
        <CodeBlock>{`php moosh.php course:list --moodle-path=/var/www/moodle`}</CodeBlock>
      </section>

      <section className="space-y-3">
        <h2 className="text-xl font-semibold">Your First Commands</h2>
        <CodeBlock>{`# List all courses
php moosh.php course:list

# List courses as JSON
php moosh.php course:list -o json

# Get site information
php moosh.php site:info

# List installed plugins
php moosh.php plugin:list

# List users
php moosh.php user:list`}</CodeBlock>
      </section>

      <section className="space-y-3">
        <h2 className="text-xl font-semibold">Getting Help</h2>
        <p className="text-muted-foreground">
          Use the <code className="bg-muted px-1.5 py-0.5 rounded text-sm">help</code> command
          or <code className="bg-muted px-1.5 py-0.5 rounded text-sm">--help</code> flag to get
          detailed information about any command:
        </p>
        <CodeBlock>{`php moosh.php help course:list
php moosh.php course:list --help`}</CodeBlock>
      </section>
    </div>
  );
}
