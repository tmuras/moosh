import { Link } from 'react-router-dom';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CodeBlock } from '@/components/CodeBlock';
import { Terminal, Layers, Settings, FileOutput, BookOpen, Zap } from 'lucide-react';
import { commands } from '@/data/commands';
import { categories } from '@/data/categories';

const features = [
  {
    icon: Terminal,
    title: '169 Commands',
    description: 'Comprehensive CLI tools covering courses, users, plugins, roles, and more.',
  },
  {
    icon: Layers,
    title: 'Version-Aware',
    description: 'Automatic handler selection based on your Moodle version (5.1+, 5.2+).',
  },
  {
    icon: Settings,
    title: 'Flexible Bootstrap',
    description: '6 bootstrap levels from no Moodle includes to full browser-context bootstrap.',
  },
  {
    icon: FileOutput,
    title: 'Multiple Output Formats',
    description: 'Table, CSV, JSON, and one-line output for easy scripting and integration.',
  },
  {
    icon: Zap,
    title: 'Built on Symfony Console',
    description: 'Modern PHP 8.2+ codebase using Symfony Console 7.x.',
  },
  {
    icon: BookOpen,
    title: 'Open Source',
    description: 'Licensed under GNU GPL v3+. Community-driven development.',
  },
];

export function HomePage() {
  return (
    <div className="space-y-12">
      <section className="text-center space-y-4 py-8">
        <div className="flex justify-center">
          <Badge variant="secondary" className="text-sm">
            {commands.length} commands &middot; {categories.length} categories
          </Badge>
        </div>
        <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">
          moosh2
        </h1>
        <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
          Moodle Shell &mdash; a command-line tool for managing Moodle installations.
          Rewritten with Symfony Console 7.x and PHP 8.2+.
        </p>
      </section>

      <section>
        <h2 className="text-lg font-semibold mb-3">Quick Start</h2>
        <CodeBlock>{`# Install
composer install

# Run a command
php moosh.php course:list --moodle-path=/path/to/moodle

# Output as JSON
php moosh.php course:list -o json

# Get help for any command
php moosh.php help course:list`}</CodeBlock>
      </section>

      <section>
        <h2 className="text-lg font-semibold mb-4">Features</h2>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {features.map((feature) => (
            <Card key={feature.title}>
              <CardHeader className="pb-2">
                <feature.icon className="h-5 w-5 text-muted-foreground mb-1" />
                <CardTitle className="text-base">{feature.title}</CardTitle>
              </CardHeader>
              <CardContent>
                <CardDescription>{feature.description}</CardDescription>
              </CardContent>
            </Card>
          ))}
        </div>
      </section>

      <section>
        <h2 className="text-lg font-semibold mb-4">Command Categories</h2>
        <div className="flex flex-wrap gap-2">
          {categories.map((cat) => {
            const count = commands.filter((c) => c.category === cat.slug).length;
            return (
              <Link key={cat.slug} to={`/commands?category=${cat.slug}`}>
                <Badge variant="outline" className="cursor-pointer hover:bg-accent text-sm">
                  {cat.label}
                  <span className="ml-1 text-muted-foreground">({count})</span>
                </Badge>
              </Link>
            );
          })}
        </div>
      </section>
    </div>
  );
}
