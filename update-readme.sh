#! /bin/bash

# Example: Get the workflow run URL from the environment
WORKFLOW_RUN_URL="${WORKFLOW_RUN_URL:-"https://unknown.com"}"
WORKFLOW_RUN_URL="https://github.com/aussieveen/tvwatchlist/actions/runs/7582473247"
# Example: Get the workflow run status from the environment
WORKFLOW_RUN_STATUS="${WORKFLOW_RUN_STATUS:-"unknown"}"
WORKFLOW_RUN_STATUS="failing"

# Example: Format test results with a badge and link based on the workflow status
if [ "${WORKFLOW_RUN_STATUS}" == "success" ]; then
    TEST_RESULTS="[![Tests](https://img.shields.io/badge/tests-passing-brightgreen)](${WORKFLOW_RUN_URL})"
else
    TEST_RESULTS="[![Tests](https://img.shields.io/badge/tests-failing-red)](${WORKFLOW_RUN_URL})"
fi

ESCAPED_TEST_RESULTS=$(printf '%s\n' "${TEST_RESULTS}" | sed -e 's/[]\/$*.^[]/\\&/g')
sed -i.bak "s/\[!\[Tests\].*/"${ESCAPED_TEST_RESULTS}"/g" README.md
