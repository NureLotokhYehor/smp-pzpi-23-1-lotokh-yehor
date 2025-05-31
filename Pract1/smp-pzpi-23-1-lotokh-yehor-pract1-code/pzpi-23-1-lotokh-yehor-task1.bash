#!/bin/bash

create_top_level() {
    local max_cells=$1
    local filled_cells=1
    local iters=0

    while [ $filled_cells -le $((max_cells)) ]; do
        local all_gaps=$((max_cells - filled_cells))
        local half_gaps=$(((all_gaps / 2) + 1))
        iters=$((iters + 1))

        printf "%*s" $half_gaps ""
        if [ $((iters % 2)) -eq 0 ]; then
            for ((i = 0; i < filled_cells; i++)); do printf '#'; done
        else
            for ((i = 0; i < filled_cells; i++)); do printf '*'; done
        fi
        echo

        filled_cells=$((filled_cells + 2))
    done
}

create_middle_level() {
    local rows=$1
    local snow_width=$2
    local max_cells=$((1 + (rows - 1) * 2 + 2))
    local filled_cells=3
    local iters=0

    until [ $filled_cells -gt $((max_cells)) ]; do
        local all_gaps=$((snow_width - filled_cells))
        local half_gaps=$(((all_gaps / 2) + 1))
        iters=$((iters + 1))

        printf "%*s" $half_gaps ""
        if [ $(((max_cells + 1) / 2 % 2)) -eq 0 ]; then
            if [ $((iters % 2)) -eq 0 ]; then
                for ((i = 0; i < filled_cells; i++)); do printf '#'; done
            else
                for ((i = 0; i < filled_cells; i++)); do printf '*'; done
            fi
        else
            if [ $((iters % 2)) -eq 0 ]; then
                for ((i = 0; i < filled_cells; i++)); do printf '*'; done
            else
                for ((i = 0; i < filled_cells; i++)); do printf '#'; done
            fi
        fi
        echo

        filled_cells=$((filled_cells + 2))
    done
}

create_low_level() {
    local max_cells=$1
    local pillar_width=3
    local all_gaps=$((max_cells - pillar_width))
    local half_gaps=$((all_gaps / 2))

    for _ in 1 2; do
        printf "%*s" $half_gaps ""
        for ((i = 0; i < pillar_width; i++)); do printf '#'; done
        echo
    done

    for ((i = 0; i < max_cells; i++)); do printf '*'; done
    echo
}

min_height() {
    local max_cells=$1
    echo $(((max_cells - 1) / 2 + 3))
}

need_height() {
    local max_cells=$1
    local first_level=$(((max_cells - 1) / 2))
    local second_level=$(((max_cells - 1) / 2 - 1))
    local third_level=3
    echo $((first_level + second_level + third_level))
}

is_width_possible() {
    local max_cells=$1
    if [ $(((max_cells + 1) % 2)) -eq 0 ]; then
        return 0
    else
        return 1
    fi
}

main() {
    if [ $# -ne 2 ]; then
        echo "Usage: $0 <rows> <snowWidth>" >&2
        exit 1
    fi

    local rows=$1
    local snow_width=$2

    if [ $snow_width -le 0 ] || [ $rows -le 0 ]; then
        echo "It is impossible to create a Christmas tree with such a height and such a width" >&2
        exit 1
    fi

    if ! is_width_possible $snow_width; then
        if is_width_possible $((snow_width - 1)); then
            snow_width=$((snow_width - 1))
        else
            echo "It is impossible to create a Christmas tree with such a height and such a width" >&2
            echo "Width error" >&2
            exit 1
        fi
    fi

    local needed_height=$(need_height $snow_width)
    if [ $rows -ne $needed_height ]; then
        if [ $((rows - 1)) -eq $needed_height ]; then
            rows=$((rows - 1))
        else
            echo "It is impossible to create a Christmas tree with such a height and such a width" >&2
            echo "Height error" >&2
            exit 1
        fi
    fi

    create_top_level $((snow_width - 2))
    create_middle_level $((rows - $(min_height $snow_width))) $((snow_width - 2))
    create_low_level $snow_width
}

main "$@"