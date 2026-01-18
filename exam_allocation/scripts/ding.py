import pandas as pd
import glob
import os

input_folder = "C:/excels/"
output_folder = "C:/excels/csv/"

os.makedirs(output_folder, exist_ok=True)

for file in glob.glob(input_folder + "*.xlsx"):
    df = pd.read_excel(file)
    
    filename = os.path.basename(file).replace(".xlsx", ".csv")
    df.to_csv(output_folder + filename, index=False)

    print(f"Converted: {filename}")

print("All files converted!")
