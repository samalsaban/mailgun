#!/bin/bash
while true
do
 tail test_err > temp_err
 cat temp_err > test_err
 
 tail bulk_err > temp_err
 cat temp_err > bulk_err
 
 tail qdel_err > temp_err
 cat temp_err > qdel_err  
 
 tail split_err > temp_err
 cat temp_err > split_err  

 rm temp_err
 sleep 3600
 
done

