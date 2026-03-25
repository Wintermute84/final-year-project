import pandas as pd
import re
import sys
import os
from datetime import datetime

if len(sys.argv) < 2:
    print("No input file provided")
    sys.exit(1)

input_file = sys.argv[1]

base = os.path.splitext(os.path.basename(input_file))[0]
output_file = f"Formatted.csv"

# Read excel file without header
df = pd.read_excel(input_file, header=None)

# Find header row
header_row_idx = -1
for i, row in df.iterrows():
    val_date = str(row[0]).strip().lower() if pd.notna(row[0]) else ""
    val_time = str(row[1]).strip().lower() if pd.notna(row[1]) else ""
    if val_date == 'date' and val_time == 'time':
        header_row_idx = i
        break

if header_row_idx == -1:
    print("Could not find header row with 'Date' and 'Time'")
    exit(1)

departments = []
for col_idx in range(2, len(df.columns)):
    dept = str(df.iloc[header_row_idx, col_idx]).strip()
    if dept and dept.lower() != 'nan':
         departments.append((col_idx, dept))

def convert_to_24hr(time_str):
    if pd.isna(time_str) or str(time_str).strip().lower() == 'nan':
        return ""
    parts = str(time_str).split('-')
    if len(parts) == 2:
        def parse_t(t):
            t = t.strip().upper().replace('.', ':')
            # Ensure there is a space before AM/PM
            t = re.sub(r'(\d)(AM|PM)', r'\1 \2', t)
            try:
                # e.g., "9:30 AM" -> "09:30"
                return datetime.strptime(t, "%I:%M %p").strftime("%H:%M")
            except ValueError:
                return t
        return f"{parse_t(parts[0])}-{parse_t(parts[1])}"
    return str(time_str).strip()

records = []
current_date = ""
current_day = ""

for i in range(header_row_idx + 1, len(df)):
    row = df.iloc[i]
    date_cell = str(row[0]).strip() if pd.notna(row[0]) else ""
    time_cell = str(row[1]).strip() if pd.notna(row[1]) else ""
    
    if date_cell.lower() == 'nan': date_cell = ""
    if time_cell.lower() == 'nan': time_cell = ""
    
    if not time_cell:
        continue # Skip rows with no time, as they hold no exams
        
    if date_cell:
        # E.g., "10-10-2025\nFRIDAY"
        parts = date_cell.split('\n')
        if len(parts) >= 2:
            current_date = parts[0].strip()
            current_day = parts[1].strip()
        else:
            m = re.match(r'(\d{2}-\d{2}-\d{4})\s*([A-Za-z]+)?', date_cell)
            if m:
                current_date = m.group(1).strip()
                current_day = m.group(2).strip() if m.group(2) else ""
            else:
                current_date = date_cell
                current_day = ""

    sess = "FN" if "AM" in time_cell.upper() else ("AN" if "PM" in time_cell.upper() else "")
    formatted_time = convert_to_24hr(time_cell)
    sem = "5"
    
    for col_idx, dept in departments:
        cell_val = str(row[col_idx]).strip() if pd.notna(row[col_idx]) else ""
        if cell_val.lower() != 'nan' and cell_val:
            # Match Course ID: 3 letters + 3 digits, e.g. CET301
            m = re.search(r'([A-Z]{3}\d{3})', cell_val)
            if m:
                cid = m.group(1)
                records.append({
                    'edate': current_date,
                    'day': current_day,
                    'time': formatted_time,
                    'sess': sess,
                    'cid': cid,
                    'sem': sem,
                    'dept': dept
                })

out_df = pd.DataFrame(records, columns=['edate', 'day', 'time', 'sess', 'cid', 'sem', 'dept'])
out_df.to_csv(output_file, index=False)
print(os.path.abspath(output_file))
