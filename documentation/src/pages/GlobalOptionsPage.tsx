import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { globalOptions } from '@/data/global-options';

export function GlobalOptionsPage() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Global Options</h1>
        <p className="text-muted-foreground mt-2">
          These options are available for all moosh2 commands.
        </p>
      </div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Option</TableHead>
            <TableHead>Shortcut</TableHead>
            <TableHead>Type</TableHead>
            <TableHead>Default</TableHead>
            <TableHead>Description</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {globalOptions.map((opt) => (
            <TableRow key={opt.name}>
              <TableCell className="font-mono text-sm whitespace-nowrap">{opt.name}</TableCell>
              <TableCell className="font-mono text-sm">
                {opt.shortcut || <span className="text-muted-foreground">&mdash;</span>}
              </TableCell>
              <TableCell>
                <Badge variant={opt.type === 'value_none' ? 'secondary' : 'outline'} className="text-xs">
                  {opt.type === 'value_none' ? 'flag' : 'value'}
                </Badge>
              </TableCell>
              <TableCell className="font-mono text-sm">
                {opt.default || <span className="text-muted-foreground">&mdash;</span>}
              </TableCell>
              <TableCell className="text-muted-foreground">{opt.description}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}
