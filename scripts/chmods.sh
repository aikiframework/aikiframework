#!/bin/sh

for d in $(find -type d); do chmod 0755 $d; done
for f in $(find -type f); do chmod 0644 $f; done
for f in $(find -name \*\.sh); do chmod 0744 $f; done
