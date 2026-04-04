import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { CodeBlock } from '@/components/CodeBlock';

export function OutputFormatsPage() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Output Formats</h1>
        <p className="text-muted-foreground mt-2">
          moosh2 supports four output formats for tabular data, controlled by the{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">--output</code> (or{' '}
          <code className="bg-muted px-1.5 py-0.5 rounded text-sm">-o</code>) flag.
        </p>
      </div>

      <Tabs defaultValue="table">
        <TabsList>
          <TabsTrigger value="table">Table</TabsTrigger>
          <TabsTrigger value="csv">CSV</TabsTrigger>
          <TabsTrigger value="json">JSON</TabsTrigger>
          <TabsTrigger value="oneline">One-line</TabsTrigger>
        </TabsList>

        <TabsContent value="table" className="space-y-3">
          <p className="text-muted-foreground">
            Default format. Renders an ASCII table using Symfony's Table helper.
          </p>
          <CodeBlock>{`$ php moosh.php course:list -o table

+----+------------+------------------+
| id | shortname  | fullname         |
+----+------------+------------------+
| 2  | CS101      | Computer Science |
| 3  | MATH200    | Linear Algebra   |
+----+------------+------------------+`}</CodeBlock>
        </TabsContent>

        <TabsContent value="csv" className="space-y-3">
          <p className="text-muted-foreground">
            Quoted CSV output, suitable for import into spreadsheets or processing with standard tools.
          </p>
          <CodeBlock>{`$ php moosh.php course:list -o csv

"id","shortname","fullname"
"2","CS101","Computer Science"
"3","MATH200","Linear Algebra"`}</CodeBlock>
        </TabsContent>

        <TabsContent value="json" className="space-y-3">
          <p className="text-muted-foreground">
            Pretty-printed JSON with Unicode support. Ideal for piping to{' '}
            <code className="bg-muted px-1 rounded text-xs">jq</code> or consuming from scripts.
          </p>
          <CodeBlock>{`$ php moosh.php course:list -o json

[
    {
        "id": "2",
        "shortname": "CS101",
        "fullname": "Computer Science"
    },
    {
        "id": "3",
        "shortname": "MATH200",
        "fullname": "Linear Algebra"
    }
]`}</CodeBlock>
        </TabsContent>

        <TabsContent value="oneline" className="space-y-3">
          <p className="text-muted-foreground">
            Space-separated first column values. Useful for piping to other commands.
          </p>
          <CodeBlock>{`$ php moosh.php course:list -o oneline

2 3`}</CodeBlock>
        </TabsContent>
      </Tabs>
    </div>
  );
}
