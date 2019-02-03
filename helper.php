<?php

function posts($where){
    return \Models\Post::select($where);
}