import { useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { useCommandSearch } from '@/hooks/use-command-search';
import { categories } from '@/data/categories';
import { Search } from 'lucide-react';

export function CommandsPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const categoryFilter = searchParams.get('category') || '';
  const [query, setQuery] = useState('');

  const results = useCommandSearch(query);
  const filtered = categoryFilter
    ? results.filter((cmd) => cmd.category === categoryFilter)
    : results;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Command Reference</h1>
        <p className="text-muted-foreground mt-2">
          Browse all {results.length} commands or search by name, category, or description.
        </p>
      </div>

      <div className="flex flex-col sm:flex-row gap-3">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Search commands..."
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            className="pl-9"
          />
        </div>
      </div>

      <div className="flex flex-wrap gap-1.5">
        <Badge
          variant={categoryFilter === '' ? 'default' : 'outline'}
          className="cursor-pointer text-xs"
          onClick={() => setSearchParams({})}
        >
          All
        </Badge>
        {categories.map((cat) => {
          const count = results.filter((c) => c.category === cat.slug).length;
          if (count === 0) return null;
          return (
            <Badge
              key={cat.slug}
              variant={categoryFilter === cat.slug ? 'default' : 'outline'}
              className="cursor-pointer text-xs"
              onClick={() =>
                setSearchParams(categoryFilter === cat.slug ? {} : { category: cat.slug })
              }
            >
              {cat.label} ({count})
            </Badge>
          );
        })}
      </div>

      <div className="grid gap-3 sm:grid-cols-2">
        {filtered.map((cmd) => (
          <Link key={cmd.name} to={`/commands/${cmd.category}/${cmd.name.split(':')[1]}`}>
            <Card className="h-full hover:bg-accent/50 transition-colors cursor-pointer">
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-mono">{cmd.name}</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-muted-foreground">{cmd.description}</p>
                <div className="flex gap-1.5 mt-2">
                  <Badge variant="secondary" className="text-xs">{cmd.category}</Badge>
                  <Badge variant="outline" className="text-xs">{cmd.bootstrapLevel}</Badge>
                </div>
              </CardContent>
            </Card>
          </Link>
        ))}
      </div>

      {filtered.length === 0 && (
        <p className="text-center text-muted-foreground py-8">
          No commands found matching your search.
        </p>
      )}
    </div>
  );
}
