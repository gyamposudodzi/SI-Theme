const fs = require("fs");
const path = require("path");

const root = process.cwd();
const builds = [
  {
    input: path.join(root, "style.css"),
    output: path.join(root, "assets/css/theme.min.css"),
  },
  {
    input: path.join(root, "assets/css/tools-page.css"),
    output: path.join(root, "assets/css/tools-page.min.css"),
  },
];

function stripComments(css) {
  let out = "";
  let inComment = false;
  let inString = false;
  let quote = "";

  for (let i = 0; i < css.length; i += 1) {
    const char = css[i];
    const next = css[i + 1];

    if (inComment) {
      if (char === "*" && next === "/") {
        inComment = false;
        i += 1;
      }
      continue;
    }

    if (inString) {
      out += char;
      if (char === "\\" && next) {
        out += next;
        i += 1;
        continue;
      }
      if (char === quote) {
        inString = false;
        quote = "";
      }
      continue;
    }

    if ((char === '"' || char === "'") && !inString) {
      inString = true;
      quote = char;
      out += char;
      continue;
    }

    if (char === "/" && next === "*") {
      inComment = true;
      i += 1;
      continue;
    }

    out += char;
  }

  return out;
}

function minifyCss(css) {
  let output = stripComments(css);

  output = output.replace(/\r\n/g, "\n");
  output = output.replace(/\s+/g, " ");
  output = output.replace(/\s*([{}:;,>])\s*/g, "$1");
  output = output.replace(/;}/g, "}");
  output = output.replace(/\s*,\s*/g, ",");

  return output.trim();
}

for (const build of builds) {
  const source = fs.readFileSync(build.input, "utf8");
  const minified = minifyCss(source);

  fs.mkdirSync(path.dirname(build.output), { recursive: true });
  fs.writeFileSync(build.output, `${minified}\n`, "utf8");
  console.log(`Minified ${path.relative(root, build.input)} -> ${path.relative(root, build.output)}`);
}
