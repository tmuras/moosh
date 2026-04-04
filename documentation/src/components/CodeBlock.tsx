interface CodeBlockProps {
  children: string;
  language?: string;
}

export function CodeBlock({ children }: CodeBlockProps) {
  return (
    <pre className="overflow-x-auto rounded-lg border bg-muted p-4 text-sm">
      <code>{children}</code>
    </pre>
  );
}
