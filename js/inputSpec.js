function parseInputSpec (inputSpec) {
  if (!inputSpec) return [];

  const params = JSON.parse(inputSpec);

  if (!Array.isArray(params)) return [];

  for (const param of params) {
    const defaultValue = param.default;
    delete param.default;

    param.value_string = !param.multiple && param.type === "String" ? String(defaultValue ?? "") : "";
    param.value_float = !param.multiple && param.type === "Float" ? Number(defaultValue ?? 0) : 0;
    param.value_boolean = !param.multiple && param.type === "Boolean" ? Boolean(defaultValue) : false;
    param.value_multiple = "[]";
  }

  return params;
};

function serializeInputSpec (inputSpec) {
  if (!Array.isArray(inputSpec)) return null;
  if (inputSpec.length < 1) return null;

  const params = structuredClone(inputSpec);

  for (const param of params) {
    param.default = param.multiple ? [] : param[`value_${param.type.toLowerCase()}`];
    delete param.value_string;
    delete param.value_float;
    delete param.value_boolean;
    delete param.value_multiple;
  }

  return JSON.stringify(params);
}
