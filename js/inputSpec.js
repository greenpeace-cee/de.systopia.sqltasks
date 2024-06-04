function parseInputSpec (inputSpec) {
  if (!inputSpec) return [];

  const params = JSON.parse(inputSpec);

  if (!Array.isArray(params)) return [];

  for (const param of params) {
    const defaultValue = param.default;
    delete param.default;

    param.value_string = param.type === "String" ? String(defaultValue ?? "") : "";
    param.value_float = param.type === "Float" ? Number(defaultValue ?? 0) : 0;
    param.value_boolean = param.type === "Boolean" ? Boolean(defaultValue) : false;
  }

  return params;
};

function serializeInputSpec (inputSpec) {
  if (!Array.isArray(inputSpec)) return null;
  if (inputSpec.length < 1) return null;

  const params = structuredClone(inputSpec);

  for (const param of params) {
    param.default = param[`value_${param.type.toLowerCase()}`];
    delete param.value_string;
    delete param.value_float;
    delete param.value_boolean;
  }

  return JSON.stringify(params);
}
