#!/bin/bash

show_help() {
    echo "Usage: $0 [--help | --version] | [[-q|--quiet] [академ_група] файл_із_cist.csv]"
    echo "Options:"
    echo "--help       Show help information"
    echo "--version    Show version information"
    echo "-q, --quiet  Suppress output to standard output"
    echo "академ_група  The academic group to process (optional)"
    echo "файл_із_cist.csv  The CSV file to process"
}

show_version() {
    echo "Version 1.0"
}

process_file() {
    local group=$1
    local file=$2

    if [[ ! -f "$file" || ! -r "$file" ]]; then
        echo "Error: File $file not found or not readable." >&2
        exit 1
    fi

    file_encoding=$(file -bi "$file" | sed 's/.*charset=//')

    if [[ "$file_encoding" != "utf-8" ]]; then
        iconv -f WINDOWS-1251 -t UTF-8 "$file" | sed 's/\r/\r\n/g' > "$file.new"
        mv "$file.new" "$file"
    fi

    sed -i 's/ПЗПІ-23-1,2,3,4,5/ПЗПІ-23-1;2;3;4;5/g' "$file"
    sed -i 's/ІМ Пз DL, DL_1/ІМ Пз DL; DL_1/g' "$file"
    sed -i 's/ІМ Конс DL, DL_1/ІМ Конс DL; DL_1/g' "$file"
    sed -i 's/ІМ Екз DL, DL_1/ІМ Екз DL; DL_1/g' "$file"
    sed -i 's/ПЗПІ-23-1;2;3;4;5/ПЗПІ 23 1;2;3;4;5/g' "$file"
    sed -i 's/ІМ Пз DL_1, DL/ІМ Пз DL_1; DL/g' "$file"

    awk -F, -v group="$group" '
    BEGIN {
        print "Subject,Start Date,Start Time,End Date,End Time,Description"
    }
    {
        split(group, group_list, ",")
        
        match_found = 0
        for (i in group_list) {
            if ($1 ~ group_list[i]) {
                match_found = 1
                break
            }
        }

        if (match_found) {
            start_date = $2
            start_time = $3
            
            gsub(/"/, "", start_date)
            gsub(/"/, "", start_time)
            split(start_date, d, ".")
            sort_date = d[3] d[2] d[1]
            date_key = d[3] d[2] d[1]
            split(start_time, t, ":")
            sort_time = sprintf("%02d%02d", t[1], t[2])
            sort_key = sort_date sort_time
            subject = $1

            gsub(/^.* - /, "", subject)
            subject = substr(subject, 1, length(subject) - 1)
            
            split(subject, parts, " ")

            category = parts[1] " " parts[2] " " parts[3]
            # Store row data
            row[NR] = $0
            sort_keys[NR] = sort_key
            categories[NR] = category
            subjects[NR] = subject
            dates[NR] = date_key
        }
    }
    END {
        n = asorti(sort_keys, sorted, "@val_str_asc")
        
        delete counters
        delete last_date
        
        # Process rows in sorted order
        for (i = 1; i <= n; i++) {
            idx = sorted[i]
            split(row[idx], fields, ",")
            
            start_date = fields[2]
            start_time = fields[3]
            end_date = fields[4]
            end_time = fields[5]
            description = fields[12]
            subject = subjects[idx]
            category = categories[idx]
            date = dates[idx]
            
            counter_key = category
            if (category ~ / Лб /) {
                if (!(counter_key in last_date) || last_date[counter_key] != date) {
                    if (!(counter_key in counters)) {
                        counters[counter_key] = 1
                    } else {
                        counters[counter_key]++
                    }
                    last_date[counter_key] = date
                }
            } else {
                if (!(counter_key in counters)) {
                    counters[counter_key] = 1
                } else {
                    counters[counter_key]++
                }
            }
            
            subject = subject " №" counters[counter_key]
            
            gsub(/"/, "", start_date)
            gsub(/"/, "", start_time)
            gsub(/"/, "", end_date)
            gsub(/"/, "", end_time)
            
            split(start_date, start, ".")
            start_formatted = start[1] "/" start[2] "/" start[3]
            
            split(end_date, end, ".")
            end_formatted = end[1] "/" end[2] "/" end[3]
            
            split(start_time, time, ":")
            start_hour = time[1]
            start_minute = time[2]
            am_pm = (start_hour < 12) ? "AM" : "PM"
            if (start_hour > 12) start_hour -= 12
            formatted_start = start_hour ":" start_minute " " am_pm
            
            split(end_time, time, ":")
            end_hour = time[1]
            end_minute = time[2]
            am_pm = (end_hour < 12) ? "AM" : "PM"
            if (end_hour > 12) end_hour -= 12
            formatted_end = end_hour ":" end_minute " " am_pm
            
            print "\"" subject "\",\"" start_formatted "\",\"" formatted_start "\",\"" end_formatted "\",\"" formatted_end "\"," description
        }
    }
    ' "$file" > "Google_TimeTable_$(date +'%d_%m_%Y').csv"

    if [[ "$quiet" != "true" ]]; then
        cat "Google_TimeTable_$(date +'%d_%m_%Y').csv"
    fi
}

quiet=false
group=""
file=""

while [[ $# -gt 0 ]]; do
    case $1 in
        --help)
            show_help
            exit 0
            ;;
        --version)
            show_version
            exit 0
            ;;
        -q|--quiet)
            quiet=true
            shift
            ;;
        *)
            if [[ -z "$group" ]]; then
                group=$1
            else
                file=$1
            fi
            shift
            ;;
    esac
done

if [[ -z "$group" || -z "$file" ]]; then
    echo "No group or file specified, displaying available files and groups..."
    
    select file in $(ls TimeTable_??_??_20??.csv | sort); do
        if [[ -z "$file" ]]; then
            echo "Invalid selection. Exiting."
            exit 1
        fi
        break
    done

    file_encoding=$(file -bi "$file" | sed 's/.*charset=//')

    if [[ "$file_encoding" != "utf-8" ]]; then
        iconv -f WINDOWS-1251 -t UTF-8 "$file" | sed 's/\r/\r\n/g' > "$file.new"
        mv "$file.new" "$file"
    fi

    groups=$(awk -F, 'NR > 1 {print $1}' "$file" | cut -d" " -f1 | sort | uniq | sed 's/[\"\'']//g')

    IFS=$'\n' read -rd '' -a groups_array <<< "$groups"

    groups_length=${#groups_array[@]}

    if [[ $groups_length -ne 1 ]]; then
        select group in "${groups_array[@]}"; do
            if [[ -z "$group" ]]; then
                echo "Invalid selection. Exiting."
                exit 1
            fi
            break
        done
    else
        group="${groups_array[0]}"
    fi
fi

process_file "$group" "$file"