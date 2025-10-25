#!/bin/bash
echo Stopping Run Id $1 >> stoplog
nohup sh pause.sh $1 &
sleep 2m
nohup sh pause.sh $1 &
sleep 2m
nohup sh pause.sh $1 &
echo Run Id $1 stopped >> stoplog


