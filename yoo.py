try:
    file = open('nonexistent_file.txt', 'r')
    content = file.read()
    file.close()

except FileNotFoundError:
    print('Error: The file you are trying to open does not exist. Please check the filename and try again.')
